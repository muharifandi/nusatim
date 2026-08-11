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

import com.nusatim.partner.core.model.ErrorType
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
                emit(ResultState.Error("Data not found", ErrorType.NOT_FOUND))
            }
        } catch (e: UnknownHostException) {
            emit(ResultState.Error("No connection", ErrorType.NETWORK))
        } catch (e: SocketTimeoutException) {
            emit(ResultState.Error("Timeout", ErrorType.TIMEOUT))
        } catch (e: IOException) {
            emit(ResultState.Error("Network error", ErrorType.IO))
        } catch (e: HttpException) {
            val errorBody = e.response()?.errorBody()?.string()
            val errorResponse = try {
                Gson().fromJson(errorBody, ErrorResponse::class.java)
            } catch (_: Exception) {
                null
            }

            val apiMessage = when {
                !errorResponse?.message.isNullOrBlank() -> errorResponse?.message
                errorResponse?.errors != null -> errorResponse.errors!!.values.flatten().joinToString("\n")
                else -> null
            }

            val type = when (e.code()) {
                401 -> ErrorType.UNAUTHORIZED
                403 -> ErrorType.FORBIDDEN
                404 -> ErrorType.NOT_FOUND
                422 -> ErrorType.INVALID_INPUT
                500 -> ErrorType.SERVER
                else -> ErrorType.UNKNOWN
            }

            emit(ResultState.Error(apiMessage ?: "HTTP Error ${e.code()}", type))
        } catch (e: Exception) {
            emit(ResultState.Error(e.message ?: "Unexpected error", ErrorType.UNKNOWN))
        }
    }
}
