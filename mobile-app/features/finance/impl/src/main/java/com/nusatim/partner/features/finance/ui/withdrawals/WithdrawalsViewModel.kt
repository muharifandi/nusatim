package com.nusatim.partner.features.finance.ui.withdrawals

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.finance.domain.usecase.GetWithdrawalBalanceUseCase
import com.nusatim.partner.features.finance.domain.usecase.GetWithdrawalDetailUseCase
import com.nusatim.partner.features.finance.domain.usecase.GetWithdrawalsUseCase
import com.nusatim.partner.features.finance.domain.usecase.RequestWithdrawalUseCase
import com.nusatim.partner.features.profile.domain.usecase.GetProfileUseCase
import com.nusatim.partner.features.finance.ui.withdrawals.state.WithdrawalsEffect
import com.nusatim.partner.features.finance.ui.withdrawals.state.WithdrawalsIntent
import com.nusatim.partner.features.finance.ui.withdrawals.state.WithdrawalsState
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import java.io.File
import javax.inject.Inject

@HiltViewModel
class WithdrawalsViewModel @Inject constructor(
    private val getWithdrawalsUseCase: GetWithdrawalsUseCase,
    private val getWithdrawalBalanceUseCase: GetWithdrawalBalanceUseCase,
    private val getWithdrawalDetailUseCase: GetWithdrawalDetailUseCase,
    private val requestWithdrawalUseCase: RequestWithdrawalUseCase,
    private val getProfileUseCase: GetProfileUseCase
) : BaseViewModel<WithdrawalsState, WithdrawalsIntent, WithdrawalsEffect>(WithdrawalsState()) {

    init {
        processIntent(WithdrawalsIntent.LoadWithdrawals())
        processIntent(WithdrawalsIntent.LoadBalance)
        loadPartnerProfile()
    }

    private fun loadPartnerProfile() {
        viewModelScope.launch {
            getProfileUseCase().collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState {
                        copy(isLoading = false, partnerProfile = result.data, error = null)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                    }
                }
            }
        }
    }

    override fun processIntent(intent: WithdrawalsIntent) {
        when (intent) {
            is WithdrawalsIntent.LoadWithdrawals -> loadWithdrawals(intent.page)
            is WithdrawalsIntent.LoadBalance -> loadBalance()
            is WithdrawalsIntent.LoadDetail -> loadDetail(intent.id)
            is WithdrawalsIntent.SubmitRequest -> submitRequest(intent.amount, intent.ktp, intent.note)
        }
    }

    private fun loadWithdrawals(page: Int) {
        viewModelScope.launch {
            getWithdrawalsUseCase(page = page).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState {
                        copy(isLoading = false, withdrawalsResponse = result.data, error = null)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { WithdrawalsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun loadBalance() {
        viewModelScope.launch {
            getWithdrawalBalanceUseCase().collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState {
                        copy(isLoading = false, balance = result.data, error = null)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                    }
                }
            }
        }
    }

    private fun loadDetail(id: Int) {
        viewModelScope.launch {
            getWithdrawalDetailUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState {
                        copy(isLoading = false, selectedDetail = result.data, error = null)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { WithdrawalsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun submitRequest(amount: Long, ktp: File, note: String?) {
        val amountBody = amount.toString().toRequestBody("text/plain".toMediaTypeOrNull())
        val noteBody = note?.toRequestBody("text/plain".toMediaTypeOrNull())
        val ktpBody = ktp.asRequestBody("image/*".toMediaTypeOrNull())
        val ktpPart = MultipartBody.Part.createFormData("ktp", ktp.name, ktpBody)

        viewModelScope.launch {
            requestWithdrawalUseCase(amountBody, ktpPart, noteBody).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false) }
                        sendEffect { WithdrawalsEffect.SuccessRequest }
                        loadWithdrawals(1)
                        loadBalance()
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { WithdrawalsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}
