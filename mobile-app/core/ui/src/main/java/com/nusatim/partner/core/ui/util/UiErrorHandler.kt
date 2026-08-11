package com.nusatim.partner.core.ui.util

import android.content.Context
import com.nusatim.partner.core.model.ErrorType
import com.nusatim.partner.core.ui.R

object UiErrorHandler {
    fun getErrorMessage(context: Context, type: ErrorType, customMessage: String? = null): String {
        return when (type) {
            ErrorType.NETWORK -> context.getString(R.string.error_no_connection)
            ErrorType.TIMEOUT -> context.getString(R.string.error_timeout)
            ErrorType.IO -> context.getString(R.string.error_io)
            ErrorType.UNAUTHORIZED -> context.getString(R.string.error_unauthorized)
            ErrorType.FORBIDDEN -> context.getString(R.string.error_forbidden)
            ErrorType.INVALID_INPUT -> customMessage ?: context.getString(R.string.error_invalid_input)
            ErrorType.SERVER -> context.getString(R.string.error_server)
            ErrorType.NOT_FOUND -> context.getString(R.string.error_not_found)
            ErrorType.UNKNOWN -> customMessage ?: context.getString(R.string.error_unexpected)
        }
    }
}
