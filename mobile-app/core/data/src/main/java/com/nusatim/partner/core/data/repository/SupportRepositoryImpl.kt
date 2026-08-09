package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.SupportRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.SupportApiService
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.SupportTicketResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class SupportRepositoryImpl @Inject constructor(
    private val apiService: SupportApiService
) : BaseRepository(), SupportRepository {

    override fun getSupportTickets(
        page: Int?,
        perPage: Int?
    ): Flow<ResultState<PagedBaseResponse<SupportTicketResponse>>> = safeNetworkCall {
        apiService.getSupportTickets(page, perPage)
    }

    override fun createSupportTicket(subject: String, description: String): Flow<ResultState<SupportTicketResponse>> = safeNetworkCall {
        val request = mapOf(
            "subject" to subject,
            "description" to description
        )
        apiService.createSupportTicket(request).data
    }

    override fun getSupportTicketDetail(id: Int): Flow<ResultState<SupportTicketResponse>> = safeNetworkCall {
        apiService.getSupportTicketDetail(id).data
    }
}
