package com.nusatim.partner.features.register.ui

import app.cash.turbine.test
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import com.nusatim.partner.features.register.domain.usecase.RegisterUseCase
import com.nusatim.partner.features.register.ui.state.RegisterEffect
import com.nusatim.partner.features.register.ui.state.RegisterIntent
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
import java.io.File

@OptIn(ExperimentalCoroutinesApi::class)
class RegisterViewModelTest {

    private lateinit var registerUseCase: RegisterUseCase
    private lateinit var viewModel: RegisterViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        registerUseCase = mockk()
        viewModel = RegisterViewModel(registerUseCase)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when data changed, state should be updated`() {
        viewModel.processIntent(RegisterIntent.NameChanged("Budi"))
        assertEquals("Budi", viewModel.state.value.name)
    }

    @Test
    fun `when register successful, navigate to login`() = runTest {
        // Arrange
        val partnerResponse = mockk<PartnerResponse>()
        val file = mockk<File>()
        every { file.name } returns "test.jpg"
        
        viewModel.processIntent(RegisterIntent.NameChanged("Budi"))
        viewModel.processIntent(RegisterIntent.EmailChanged("budi@mail.com"))
        viewModel.processIntent(RegisterIntent.ProfilePhotoPicked(file))
        viewModel.processIntent(RegisterIntent.KtpPicked(file))
        viewModel.processIntent(RegisterIntent.AgreementChanged(true))

        coEvery {
            registerUseCase(any(), any(), any(), any(), any(), any(), any(), any(), any(), any(), any())
        } returns flowOf(
            ResultState.Loading,
            ResultState.Success(partnerResponse)
        )

        // Act
        viewModel.effect.test {
            viewModel.processIntent(RegisterIntent.Submit)
            
            // Assert
            assertEquals(RegisterEffect.NavigateToLogin, awaitItem())
            assertEquals(true, viewModel.state.value.isSuccess)
        }
    }

    @Test
    fun `when register fails, state should show error`() = runTest {
        val file = mockk<File>()
        every { file.name } returns "test.jpg"
        viewModel.processIntent(RegisterIntent.ProfilePhotoPicked(file))
        viewModel.processIntent(RegisterIntent.KtpPicked(file))

        coEvery {
            registerUseCase(any(), any(), any(), any(), any(), any(), any(), any(), any(), any(), any())
        } returns flowOf(
            ResultState.Loading,
            ResultState.Error("Email already taken")
        )

        viewModel.processIntent(RegisterIntent.Submit)

        assertEquals(false, viewModel.state.value.isLoading)
        assertEquals("Email already taken", viewModel.state.value.error)
    }
}
