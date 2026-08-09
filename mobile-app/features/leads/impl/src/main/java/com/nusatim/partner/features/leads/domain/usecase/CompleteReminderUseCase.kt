package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LeadReminderResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class CompleteReminderUseCase @Inject constructor(
    private val repository: LeadsRepository
) {
    operator fun invoke(leadId: Int, reminderId: Int): Flow<ResultState<LeadReminderResponse>> {
        return repository.completeReminder(leadId, reminderId)
    }
}
