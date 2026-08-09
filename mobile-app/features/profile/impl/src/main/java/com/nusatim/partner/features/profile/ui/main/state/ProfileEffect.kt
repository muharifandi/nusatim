package com.nusatim.partner.features.profile.ui.main.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface ProfileEffect : UiEffect {
    data class ShowToast(val message: String) : ProfileEffect
    data object SuccessUpdate : ProfileEffect
    data object LogoutSuccess : ProfileEffect
}
