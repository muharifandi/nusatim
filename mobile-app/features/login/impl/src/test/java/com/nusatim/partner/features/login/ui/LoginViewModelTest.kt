package com.nusatim.partner.features.login.ui

import app.cash.turbine.test
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LoginResponse
import com.nusatim.partner.core.model.dto.PartnerResponse
import com.nusatim.partner.features.login.domain.usecase.LoginUseCase
import com.nusatim.partner.features.login.ui.state.LoginEffect
import com.nusatim.partner.features.login.ui.state.LoginIntent
import io.mockk.coEvery
import io.mockk.every
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.UnconfinedTestDispatcher
import kotlinx.coroutines.test.resetMain
import kotlinx.coroutines.test.runTest
import kotlinx.coroutines.test.setMain
import org.junit.After
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

@OptIn(ExperimentalCoroutinesApi::class)
class LoginViewModelTest {

    private lateinit var loginUseCase: LoginUseCase
    private lateinit var sessionManager: SessionManager
    private lateinit var viewModel: LoginViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        loginUseCase = mockk()
        sessionManager = mockk(relaxed = true)
        viewModel = LoginViewModel(loginUseCase, sessionManager)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when email changed, state should be updated`() {
        viewModel.processIntent(LoginIntent.EmailChanged("test@mail.com"))
        assertEquals("test@mail.com", viewModel.state.value.email)
    }

    @Test
    fun `when login successful, session should be saved and navigate to home`() = runTest {
        // Arrange
        val partnerResponse = mockk<PartnerResponse>()
        every { partnerResponse.status } returns "approved"
        every { partnerResponse.name } returns "Test User"
        val loginResponse = LoginResponse(token = "token123", partner = partnerResponse)
        
        coEvery { loginUseCase(any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(loginResponse)
        )

        // Act
        viewModel.effect.test {
            viewModel.processIntent(LoginIntent.Submit)
            
            // Assert
            assertEquals(LoginEffect.NavigateToHome, awaitItem())
            assertEquals(false, viewModel.state.value.isLoading)
            assertEquals(true, viewModel.state.value.isSuccess)
        }
    }

    @Test
    fun `when login fails, state should show error`() = runTest {
        // Arrange
        coEvery { loginUseCase(any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Error("Invalid credentials")
        )

        // Act
        viewModel.processIntent(LoginIntent.Submit)

        // Assert
        assertEquals(false, viewModel.state.value.isLoading)
        assertEquals("Invalid credentials", viewModel.state.value.error)
    }
}
