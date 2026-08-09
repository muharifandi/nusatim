package com.nusatim.partner.features.leads.ui.customers.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse

data class CustomersState(
    val isLoading: Boolean = false,
    val customersResponse: PagedBaseResponse<CustomerResponse>? = null,
    val error: String? = null
) : UiState
