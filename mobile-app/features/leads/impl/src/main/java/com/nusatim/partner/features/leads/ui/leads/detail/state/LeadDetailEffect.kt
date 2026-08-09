package com.nusatim.partner.features.leads.ui.leads.detail.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface LeadDetailEffect : UiEffect {
    data class ShowError(val message: String) : LeadDetailEffect
    data object Success : LeadDetailEffect
    data class NavigateToCustomerDetail(val customerId: Int) : LeadDetailEffect
}
