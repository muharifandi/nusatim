package com.nusatim.partner.features.finance.domain.usecase

import com.nusatim.partner.core.domain.repository.WithdrawalsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import okhttp3.RequestBody
import javax.inject.Inject

class RequestWithdrawalUseCase @Inject constructor(
    private val repository: WithdrawalsRepository
) {
    operator fun invoke(
        amount: RequestBody,
        ktp: MultipartBody.Part,
        note: RequestBody? = null
    ): Flow<ResultState<WithdrawalResponse>> {
        return repository.requestWithdrawal(amount, ktp, note)
    }
}
