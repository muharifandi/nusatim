package com.nusatim.partner.features.leads.ui.customers.detail.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface CustomerDetailIntent : UiIntent {
    data class LoadCustomer(val id: Int) : CustomerDetailIntent
    data class UpdateCustomer(val id: Int, val request: Map<String, Any?>) : CustomerDetailIntent
    data class UpdateProgress(val id: Int, val progress: Int) : CustomerDetailIntent
}
