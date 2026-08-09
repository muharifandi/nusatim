package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class DeleteLeadDocumentUseCase @Inject constructor(
    private val repository: LeadsRepository
) {
    operator fun invoke(leadId: Int, documentId: Int): Flow<ResultState<Unit>> {
        return repository.deleteLeadDocument(leadId, documentId)
    }
}
