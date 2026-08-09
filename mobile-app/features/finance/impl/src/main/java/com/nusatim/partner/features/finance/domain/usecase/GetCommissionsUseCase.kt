package com.nusatim.partner.features.finance.domain.usecase

import com.nusatim.partner.core.domain.repository.CommissionsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CommissionResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetCommissionsUseCase @Inject constructor(
    private val repository: CommissionsRepository
) {
    operator fun invoke(
        status: String? = null,
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<CommissionResponse>>> {
        return repository.getCommissions(status, page, perPage)
    }
}
