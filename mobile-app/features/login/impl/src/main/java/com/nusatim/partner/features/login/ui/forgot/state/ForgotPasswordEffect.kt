package com.nusatim.partner.features.login.ui.forgot.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface ForgotPasswordEffect : UiEffect {
    data class NavigateToResetPassword(val email: String) : ForgotPasswordEffect
}
