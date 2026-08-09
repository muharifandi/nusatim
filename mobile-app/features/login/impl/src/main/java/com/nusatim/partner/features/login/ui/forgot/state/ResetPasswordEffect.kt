package com.nusatim.partner.features.login.ui.forgot.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface ResetPasswordEffect : UiEffect {
    data object NavigateToLogin : ResetPasswordEffect
}
