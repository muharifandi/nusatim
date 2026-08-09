package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.WithdrawalBalanceResponse
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import okhttp3.RequestBody

interface WithdrawalsRepository {
    fun getBalance(): Flow<ResultState<WithdrawalBalanceResponse>>

    fun getWithdrawals(
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<WithdrawalResponse>>>

    fun getWithdrawalDetail(id: Int): Flow<ResultState<WithdrawalResponse>>

    fun requestWithdrawal(
        amount: RequestBody,
        ktp: MultipartBody.Part,
        note: RequestBody? = null
    ): Flow<ResultState<WithdrawalResponse>>
}
