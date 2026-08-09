package com.nusatim.partner.features.splash.ui.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface SplashIntent : UiIntent {
    data object CheckSession : SplashIntent
    data object Retry : SplashIntent
}
