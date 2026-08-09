package com.nusatim.partner.features.finance.domain.usecase

import com.nusatim.partner.core.domain.repository.WithdrawalsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetWithdrawalsUseCase @Inject constructor(
    private val repository: WithdrawalsRepository
) {
    operator fun invoke(page: Int? = null, perPage: Int? = null): Flow<ResultState<PagedBaseResponse<WithdrawalResponse>>> {
        return repository.getWithdrawals(page, perPage)
    }
}
