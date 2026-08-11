package com.nusatim.partner.core.network.util

import com.nusatim.partner.core.model.ErrorType
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
                emit(ResultState.Error("Data not found", ErrorType.NOT_FOUND))
            }
        } catch (throwable: Throwable) {
            val type = when (throwable) {
                is UnknownHostException -> ErrorType.NETWORK
                is SocketTimeoutException -> ErrorType.TIMEOUT
                is IOException -> ErrorType.IO
                is HttpException -> {
                    when (throwable.code()) {
                        401 -> ErrorType.UNAUTHORIZED
                        403 -> ErrorType.FORBIDDEN
                        422 -> ErrorType.INVALID_INPUT
                        500 -> ErrorType.SERVER
                        404 -> ErrorType.NOT_FOUND
                        else -> ErrorType.UNKNOWN
                    }
                }
                else -> ErrorType.UNKNOWN
            }

            val errorBody = (throwable as? HttpException)?.response()?.errorBody()?.string()
            val errorResponse = try {
                Gson().fromJson(errorBody, ErrorResponse::class.java)
            } catch (e: Exception) {
                null
            }

            val message = errorResponse?.message ?: throwable.message ?: "Unknown Error"
            emit(ResultState.Error(message, type))
        }
    }
}
