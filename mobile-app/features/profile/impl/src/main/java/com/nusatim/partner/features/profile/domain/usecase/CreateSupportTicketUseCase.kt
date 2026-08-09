package com.nusatim.partner.features.profile.domain.usecase

import com.nusatim.partner.core.domain.repository.SupportRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.SupportTicketResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class CreateSupportTicketUseCase @Inject constructor(
    private val repository: SupportRepository
) {
    operator fun invoke(subject: String, description: String): Flow<ResultState<SupportTicketResponse>> {
        return repository.createSupportTicket(subject, description)
    }
}
