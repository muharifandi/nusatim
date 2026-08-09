package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.WithdrawalsRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.WithdrawalsApiService
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.WithdrawalBalanceResponse
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import okhttp3.RequestBody
import javax.inject.Inject

class WithdrawalsRepositoryImpl @Inject constructor(
    private val apiService: WithdrawalsApiService
) : BaseRepository(), WithdrawalsRepository {

    override fun getBalance(): Flow<ResultState<WithdrawalBalanceResponse>> = safeNetworkCall {
        apiService.getBalance()
    }

    override fun getWithdrawals(
        page: Int?,
        perPage: Int?
    ): Flow<ResultState<PagedBaseResponse<WithdrawalResponse>>> = safeNetworkCall {
        apiService.getWithdrawals(page, perPage)
    }

    override fun getWithdrawalDetail(id: Int): Flow<ResultState<WithdrawalResponse>> = safeNetworkCall {
        apiService.getWithdrawalDetail(id).data
    }

    override fun requestWithdrawal(
        amount: RequestBody,
        ktp: MultipartBody.Part,
        note: RequestBody?
    ): Flow<ResultState<WithdrawalResponse>> = safeNetworkCall {
        apiService.requestWithdrawal(amount, ktp, note).data
    }
}
