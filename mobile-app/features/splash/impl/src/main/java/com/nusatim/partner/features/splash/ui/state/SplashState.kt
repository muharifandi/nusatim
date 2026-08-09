package com.nusatim.partner.features.splash.ui.state

import com.nusatim.partner.core.architecture.mvi.UiState
import javax.annotation.concurrent.Immutable

@Immutable
data class SplashState(
    val isLoading: Boolean = true
) : UiState
