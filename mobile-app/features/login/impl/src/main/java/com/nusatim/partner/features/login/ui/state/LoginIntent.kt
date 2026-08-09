package com.nusatim.partner.features.login.ui.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface LoginIntent : UiIntent {
    data object LoadInitialData : LoginIntent
    data class EmailChanged(val value: String) : LoginIntent
    data class PasswordChanged(val value: String) : LoginIntent
    data object Submit : LoginIntent
    data object NavigateToRegister : LoginIntent
    data object NavigateToForgotPassword : LoginIntent
    data object DismissError : LoginIntent
}
