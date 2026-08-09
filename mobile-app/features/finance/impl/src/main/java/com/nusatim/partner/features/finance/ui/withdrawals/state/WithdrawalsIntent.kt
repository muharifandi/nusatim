package com.nusatim.partner.features.finance.ui.withdrawals.state

import com.nusatim.partner.core.architecture.mvi.UiIntent
import java.io.File

sealed interface WithdrawalsIntent : UiIntent {
    data class LoadWithdrawals(val page: Int = 1) : WithdrawalsIntent
    data object LoadBalance : WithdrawalsIntent
    data class LoadDetail(val id: Int) : WithdrawalsIntent
    data class SubmitRequest(val amount: Long, val ktp: File, val note: String?) : WithdrawalsIntent
}
