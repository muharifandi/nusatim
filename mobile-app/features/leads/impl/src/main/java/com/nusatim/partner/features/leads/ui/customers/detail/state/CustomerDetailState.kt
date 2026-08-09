package com.nusatim.partner.features.leads.ui.customers.detail.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.CustomerResponse

data class CustomerDetailState(
    val isLoading: Boolean = false,
    val customer: CustomerResponse? = null,
    val error: String? = null
) : UiState
