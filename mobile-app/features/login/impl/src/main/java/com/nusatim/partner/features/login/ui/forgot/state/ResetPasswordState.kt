package com.nusatim.partner.features.login.ui.forgot.state

import com.nusatim.partner.core.architecture.mvi.UiState

data class ResetPasswordState(
    val email: String = "",
    val code: String = "",
    val password: String = "",
    val passwordConfirmation: String = "",
    val isLoading: Boolean = false,
    val isSuccess: Boolean = false,
    val error: String? = null
) : UiState
