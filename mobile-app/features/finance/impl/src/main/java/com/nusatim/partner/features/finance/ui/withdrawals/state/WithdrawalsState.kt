package com.nusatim.partner.features.finance.ui.withdrawals.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.PartnerResponse
import com.nusatim.partner.core.model.dto.WithdrawalBalanceResponse
import com.nusatim.partner.core.model.dto.WithdrawalResponse

data class WithdrawalsState(
    val isLoading: Boolean = false,
    val withdrawalsResponse: PagedBaseResponse<WithdrawalResponse>? = null,
    val balance: WithdrawalBalanceResponse? = null,
    val partnerProfile: PartnerResponse? = null,
    val selectedDetail: WithdrawalResponse? = null,
    val error: String? = null
) : UiState
