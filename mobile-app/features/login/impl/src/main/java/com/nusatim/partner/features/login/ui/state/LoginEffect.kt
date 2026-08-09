package com.nusatim.partner.features.login.ui.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface LoginEffect : UiEffect {
    data object NavigateToHome : LoginEffect
    data object NavigateToApprovalStatus : LoginEffect
    data object NavigateToRegister : LoginEffect
    data object NavigateToForgotPassword : LoginEffect
}
