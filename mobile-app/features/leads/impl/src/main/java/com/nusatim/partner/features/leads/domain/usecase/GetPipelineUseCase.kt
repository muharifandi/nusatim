package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PipelineResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetPipelineUseCase @Inject constructor(
    private val repository: LeadsRepository
) {
    operator fun invoke(
        serviceId: Int? = null,
        dateFrom: String? = null,
        dateTo: String? = null
    ): Flow<ResultState<PipelineResponse>> {
        return repository.getPipeline(serviceId, dateFrom, dateTo)
    }
}
