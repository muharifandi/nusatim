package com.nusatim.partner.features.splash.ui.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface SplashEffect : UiEffect {
    data object NavigateToIntro : SplashEffect
    data object NavigateToLogin : SplashEffect
    data object NavigateToHome : SplashEffect
    data object NavigateToApprovalStatus : SplashEffect
    data class ShowError(val message: String) : SplashEffect
}
