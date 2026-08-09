package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LeadResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetLeadDetailUseCase @Inject constructor(
    private val repository: LeadsRepository
) {
    operator fun invoke(id: Int): Flow<ResultState<LeadResponse>> {
        return repository.getLeadDetail(id)
    }
}
