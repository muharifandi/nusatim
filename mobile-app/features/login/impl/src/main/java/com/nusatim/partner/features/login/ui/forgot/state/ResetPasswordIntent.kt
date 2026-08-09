package com.nusatim.partner.features.login.ui.forgot.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface ResetPasswordIntent : UiIntent {
    data class EmailChanged(val value: String) : ResetPasswordIntent
    data class CodeChanged(val value: String) : ResetPasswordIntent
    data class PasswordChanged(val value: String) : ResetPasswordIntent
    data class PasswordConfirmationChanged(val value: String) : ResetPasswordIntent
    data object Submit : ResetPasswordIntent
    data object DismissError : ResetPasswordIntent
}
