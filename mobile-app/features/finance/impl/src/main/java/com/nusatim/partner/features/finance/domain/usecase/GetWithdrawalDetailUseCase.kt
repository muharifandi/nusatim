package com.nusatim.partner.features.finance.domain.usecase

import com.nusatim.partner.core.domain.repository.WithdrawalsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetWithdrawalDetailUseCase @Inject constructor(
    private val repository: WithdrawalsRepository
) {
    operator fun invoke(id: Int): Flow<ResultState<WithdrawalResponse>> {
        return repository.getWithdrawalDetail(id)
    }
}
