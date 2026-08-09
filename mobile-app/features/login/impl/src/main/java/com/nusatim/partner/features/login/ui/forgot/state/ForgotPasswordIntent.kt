package com.nusatim.partner.features.login.ui.forgot.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface ForgotPasswordIntent : UiIntent {
    data class EmailChanged(val value: String) : ForgotPasswordIntent
    data object Submit : ForgotPasswordIntent
    data object DismissError : ForgotPasswordIntent
}
