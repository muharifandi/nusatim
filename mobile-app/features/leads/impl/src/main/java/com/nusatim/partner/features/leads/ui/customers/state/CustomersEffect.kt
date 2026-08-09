package com.nusatim.partner.features.leads.ui.customers.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface CustomersEffect : UiEffect {
    data class ShowError(val message: String) : CustomersEffect
}
