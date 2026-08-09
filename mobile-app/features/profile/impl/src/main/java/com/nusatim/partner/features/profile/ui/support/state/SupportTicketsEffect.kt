package com.nusatim.partner.features.profile.ui.support.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface SupportTicketsEffect : UiEffect {
    data class ShowError(val message: String) : SupportTicketsEffect
    data object SuccessCreate : SupportTicketsEffect
}
