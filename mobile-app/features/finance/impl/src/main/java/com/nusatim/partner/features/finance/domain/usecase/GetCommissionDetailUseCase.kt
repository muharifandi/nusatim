package com.nusatim.partner.features.finance.domain.usecase

import com.nusatim.partner.core.domain.repository.CommissionsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CommissionResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetCommissionDetailUseCase @Inject constructor(
    private val repository: CommissionsRepository
) {
    operator fun invoke(id: Int): Flow<ResultState<CommissionResponse>> {
        return repository.getCommissionDetail(id)
    }
}
