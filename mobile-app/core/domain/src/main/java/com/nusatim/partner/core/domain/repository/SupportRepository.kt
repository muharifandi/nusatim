package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.SupportTicketResponse
import kotlinx.coroutines.flow.Flow

interface SupportRepository {
    fun getSupportTickets(
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<SupportTicketResponse>>>

    fun createSupportTicket(subject: String, description: String): Flow<ResultState<SupportTicketResponse>>

    fun getSupportTicketDetail(id: Int): Flow<ResultState<SupportTicketResponse>>
}
