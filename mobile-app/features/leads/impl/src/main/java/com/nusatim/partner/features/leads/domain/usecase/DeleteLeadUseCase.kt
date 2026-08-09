package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class DeleteLeadUseCase @Inject constructor(
    private val repository: LeadsRepository
) {
    operator fun invoke(id: Int): Flow<ResultState<Unit>> {
        return repository.deleteLead(id)
    }
}
