package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetLeadsUseCase @Inject constructor(
    private val repository: LeadsRepository
) {
    operator fun invoke(
        status: String? = null,
        serviceId: Int? = null,
        search: String? = null,
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<LeadResponse>>> {
        return repository.getLeads(status, serviceId, search, page, perPage)
    }
}
