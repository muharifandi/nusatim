package com.nusatim.partner.features.finance.ui

import androidx.fragment.app.testing.launchFragmentInContainer
import androidx.test.ext.junit.runners.AndroidJUnit4
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.WithdrawalBalanceResponse
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import com.nusatim.partner.features.finance.domain.usecase.GetWithdrawalBalanceUseCase
import com.nusatim.partner.features.finance.domain.usecase.GetWithdrawalsUseCase
import com.nusatim.partner.features.finance.robot.finance
import com.nusatim.partner.features.finance.ui.withdrawals.WithdrawalsFragment
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.flow.flowOf
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class WithdrawalsFragmentTest {

    private val getWithdrawalsUseCase: GetWithdrawalsUseCase = mockk()
    private val getWithdrawalBalanceUseCase: GetWithdrawalBalanceUseCase = mockk()

    @Test
    fun displayWithdrawalList() {
        val balance = WithdrawalBalanceResponse(2000000, 100000)
        val withdrawalsResponse = PagedBaseResponse<WithdrawalResponse>(
            data = emptyList(),
            links = mockk(),
            meta = mockk()
        )
        
        coEvery { getWithdrawalBalanceUseCase() } returns flowOf(ResultState.Success(balance))
        coEvery { getWithdrawalsUseCase(any(), any()) } returns flowOf(ResultState.Success(withdrawalsResponse))

        launchFragmentInContainer<WithdrawalsFragment>()

        finance {
            verifyWithdrawalsListIsDisplayed()
        }
    }
}
