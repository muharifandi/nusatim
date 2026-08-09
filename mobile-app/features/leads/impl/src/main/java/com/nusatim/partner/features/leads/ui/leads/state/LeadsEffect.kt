package com.nusatim.partner.features.leads.ui.leads.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface LeadsEffect : UiEffect {
    data class ShowError(val message: String) : LeadsEffect
    data class NavigateToDetail(val leadId: Int) : LeadsEffect
}
