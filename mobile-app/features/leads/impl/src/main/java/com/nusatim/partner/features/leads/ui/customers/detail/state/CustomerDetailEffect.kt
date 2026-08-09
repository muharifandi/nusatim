package com.nusatim.partner.features.leads.ui.customers.detail.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface CustomerDetailEffect : UiEffect {
    data class ShowError(val message: String) : CustomerDetailEffect
    data object SuccessUpdate : CustomerDetailEffect
}
