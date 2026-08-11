/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : core:model
 * File : ResultState.kt
 *
 * Description:
 * Sealed interface untuk menangani berbagai status hasil operasi (Loading, Success, Error).
 */

package com.nusatim.partner.core.model

sealed interface ResultState<out T> {
    data object Loading : ResultState<Nothing>
    data class Success<out T>(val data: T) : ResultState<T>
    data class Error(
        val message: String,
        val cause: ErrorType = ErrorType.UNKNOWN
    ) : ResultState<Nothing>
}

enum class ErrorType {
    NETWORK, TIMEOUT, IO, UNAUTHORIZED, FORBIDDEN, INVALID_INPUT, SERVER, NOT_FOUND, UNKNOWN
}
