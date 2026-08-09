package com.nusatim.partner.features.finance.ui.commissions.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.CommissionResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse

data class CommissionsState(
    val isLoading: Boolean = false,
    val commissionsResponse: PagedBaseResponse<CommissionResponse>? = null,
    val selectedCommission: CommissionResponse? = null,
    val statusFilter: String? = null,
    val error: String? = null
) : UiState
