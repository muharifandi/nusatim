package com.nusatim.partner.features.login.ui.forgot

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.login.domain.usecase.ForgotPasswordUseCase
import com.nusatim.partner.features.login.ui.forgot.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ForgotPasswordViewModel @Inject constructor(
    private val forgotPasswordUseCase: ForgotPasswordUseCase
) : BaseViewModel<ForgotPasswordState, ForgotPasswordIntent, ForgotPasswordEffect>(ForgotPasswordState()) {

    override fun processIntent(intent: ForgotPasswordIntent) {
        when (intent) {
            is ForgotPasswordIntent.EmailChanged -> {
                setState { copy(email = intent.value, error = null) }
            }
            is ForgotPasswordIntent.Submit -> {
                submit()
            }
            is ForgotPasswordIntent.DismissError -> {
                setState { copy(error = null) }
            }
        }
    }

    private fun submit() {
        val email = state.value.email

        if (!android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            setState { copy(error = "Format email tidak valid.") }
            return
        }

        viewModelScope.launch {
            forgotPasswordUseCase(email).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true, error = null) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false, isSuccess = true) }
                        sendEffect { ForgotPasswordEffect.NavigateToResetPassword(email) }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                    }
                }
            }
        }
    }
}
