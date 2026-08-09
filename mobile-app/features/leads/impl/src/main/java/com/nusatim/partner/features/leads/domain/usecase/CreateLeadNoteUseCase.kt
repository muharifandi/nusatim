package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LeadActivityResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class CreateLeadNoteUseCase @Inject constructor(
    private val repository: LeadsRepository
) {
    operator fun invoke(id: Int, note: String): Flow<ResultState<LeadActivityResponse>> {
        return repository.createLeadNote(id, note)
    }
}
