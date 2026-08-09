/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : core:common
 * File : BaseRepository.kt
 *
 * Description:
 * Kelas dasar Repository untuk melakukan standardisasi penanganan error dan emisi flow di seluruh fitur.
 */

package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import android.util.Log
import com.google.gson.Gson
import com.nusatim.partner.core.model.dto.ErrorResponse
import retrofit2.HttpException
import java.net.UnknownHostException
import java.net.SocketTimeoutException
import java.io.IOException
abstract class BaseRepository {

    protected fun <T> safeNetworkCall(
        call: suspend () -> T
    ): Flow<ResultState<T>> = flow {
        emit(ResultState.Loading)
        try {
            val response = call()
            if (response != null) {
                emit(ResultState.Success(response))
            } else {
                emit(ResultState.Error("Terjadi kesalahan: Data tidak ditemukan."))
            }
        } catch (e: UnknownHostException) {
            Log.e("BaseRepository", "No Internet Connection", e)
            emit(ResultState.Error("Tidak ada koneksi internet. Silakan periksa jaringan Anda."))
        } catch (e: SocketTimeoutException) {
            Log.e("BaseRepository", "Connection Timeout", e)
            emit(ResultState.Error("Koneksi ke server terputus (timeout). Silakan coba lagi nanti."))
        } catch (e: IOException) {
            Log.e("BaseRepository", "Network error", e)
            emit(ResultState.Error("Terjadi kesalahan jaringan. Silakan coba beberapa saat lagi."))
        } catch (e: HttpException) {
            val errorBody = e.response()?.errorBody()?.string()
            val errorResponse = try {
                Gson().fromJson(errorBody, ErrorResponse::class.java)
            } catch (_: Exception) {
                null
            }

            if (e.code() == 422) {
                Log.w("BaseRepository", "Validation Error (422): $errorBody")
            } else {
                Log.e("BaseRepository", "HTTP error (${e.code()})", e)
            }

            val apiMessage = when {
                !errorResponse?.message.isNullOrBlank() -> errorResponse?.message
                errorResponse?.errors != null -> errorResponse.errors!!.values.flatten().joinToString("\n")
                else -> null
            }

            val errorMessage = when (e.code()) {
                401 -> apiMessage ?: "Sesi Anda telah berakhir. Silakan login kembali."
                403 -> apiMessage ?: "Anda tidak memiliki akses untuk melakukan tindakan ini."
                404 -> apiMessage ?: "Resource tidak ditemukan (404)."
                422 -> {
                    val fieldErrors = errorResponse?.errors?.values?.flatten()?.joinToString("\n")
                    fieldErrors ?: apiMessage ?: "Data yang Anda masukkan tidak valid."
                }
                500 -> apiMessage ?: "Server sedang mengalami gangguan. Silakan coba beberapa saat lagi."
                else -> apiMessage ?: "Terjadi kesalahan sistem (${e.code()})"
            }
            emit(ResultState.Error(errorMessage))
        } catch (e: Exception) {
            Log.e("BaseRepository", "Unexpected error", e)
            emit(ResultState.Error(e.message ?: "Terjadi kesalahan yang tidak terduga."))
        }
    }
}
