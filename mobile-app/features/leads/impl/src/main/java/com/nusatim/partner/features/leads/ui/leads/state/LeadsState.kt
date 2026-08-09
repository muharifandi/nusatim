package com.nusatim.partner.features.leads.ui.leads.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse

data class LeadsState(
    val isLoading: Boolean = false,
    val leadsResponse: PagedBaseResponse<LeadResponse>? = null,
    val error: String? = null,
    val searchQuery: String? = null,
    val statusFilter: String? = null
) : UiState
