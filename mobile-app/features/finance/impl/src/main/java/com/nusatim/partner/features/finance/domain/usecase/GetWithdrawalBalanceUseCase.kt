package com.nusatim.partner.features.finance.domain.usecase

import com.nusatim.partner.core.domain.repository.WithdrawalsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.WithdrawalBalanceResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetWithdrawalBalanceUseCase @Inject constructor(
    private val repository: WithdrawalsRepository
) {
    operator fun invoke(): Flow<ResultState<WithdrawalBalanceResponse>> {
        return repository.getBalance()
    }
}
