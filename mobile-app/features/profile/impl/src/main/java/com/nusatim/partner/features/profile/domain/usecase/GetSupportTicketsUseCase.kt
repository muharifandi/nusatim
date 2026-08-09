package com.nusatim.partner.features.profile.domain.usecase

import com.nusatim.partner.core.domain.repository.SupportRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.SupportTicketResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetSupportTicketsUseCase @Inject constructor(
    private val repository: SupportRepository
) {
    operator fun invoke(page: Int? = null, perPage: Int? = null): Flow<ResultState<PagedBaseResponse<SupportTicketResponse>>> {
        return repository.getSupportTickets(page, perPage)
    }
}
