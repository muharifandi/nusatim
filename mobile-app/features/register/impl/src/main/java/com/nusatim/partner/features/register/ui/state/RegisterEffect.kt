package com.nusatim.partner.features.register.ui.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface RegisterEffect : UiEffect {
    data object NavigateToLogin : RegisterEffect
    data class ShowError(val message: String) : RegisterEffect
}
