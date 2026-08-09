package com.nusatim.partner.core.network.util

import com.nusatim.partner.core.model.ResultState
import com.google.gson.Gson
import com.nusatim.partner.core.model.dto.ErrorResponse
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import retrofit2.HttpException
import java.io.IOException
import java.net.SocketTimeoutException
import java.net.UnknownHostException
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class SafeApiCall @Inject constructor() {
    fun <T> flow(apiCall: suspend () -> T): Flow<ResultState<T>> = kotlinx.coroutines.flow.flow {
        emit(ResultState.Loading)
        try {
            val response = apiCall.invoke()
            if (response != null) {
                emit(ResultState.Success(response))
            } else {
                emit(ResultState.Error("Terjadi kesalahan: Data tidak ditemukan."))
            }
        } catch (throwable: Throwable) {
            val message = when (throwable) {
                is UnknownHostException -> "Tidak ada koneksi internet. Silakan periksa jaringan Anda."
                is SocketTimeoutException -> "Koneksi ke server terputus (timeout). Silakan coba lagi."
                is IOException -> "Terjadi kesalahan jaringan saat mengambil data."
                is HttpException -> {
                    val errorBody = throwable.response()?.errorBody()?.string()
                    val errorResponse = try {
                        Gson().fromJson(errorBody, ErrorResponse::class.java)
                    } catch (e: Exception) {
                        null
                    }

                    when (throwable.code()) {
                        401 -> "Sesi Anda telah berakhir. Silakan login kembali."
                        403 -> "Anda tidak memiliki akses untuk melakukan tindakan ini."
                        422 -> errorResponse?.message ?: "Data yang Anda masukkan tidak valid."
                        500 -> "Server sedang mengalami gangguan. Silakan coba beberapa saat lagi."
                        else -> errorResponse?.message ?: "Terjadi kesalahan sistem (${throwable.code()})"
                    }
                }
                else -> throwable.message ?: "Terjadi kesalahan yang tidak terduga"
            }
            emit(ResultState.Error(message))
        }
    }
}
