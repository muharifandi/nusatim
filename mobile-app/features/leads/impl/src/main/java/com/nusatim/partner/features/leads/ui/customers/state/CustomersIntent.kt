package com.nusatim.partner.features.leads.ui.customers.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface CustomersIntent : UiIntent {
    data class LoadCustomers(val page: Int = 1) : CustomersIntent
}
