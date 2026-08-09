package com.nusatim.partner.features.profile.ui.support.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.SupportTicketResponse

data class SupportTicketsState(
    val isLoading: Boolean = false,
    val ticketsResponse: PagedBaseResponse<SupportTicketResponse>? = null,
    val selectedDetail: SupportTicketResponse? = null,
    val error: String? = null
) : UiState
