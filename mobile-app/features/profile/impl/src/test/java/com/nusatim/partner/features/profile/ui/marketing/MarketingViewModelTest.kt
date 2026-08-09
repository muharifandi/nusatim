package com.nusatim.partner.features.profile.ui.marketing

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse
import com.nusatim.partner.features.profile.domain.usecase.GetMarketingMaterialDetailUseCase
import com.nusatim.partner.features.profile.domain.usecase.GetMarketingMaterialsUseCase
import com.nusatim.partner.features.profile.ui.marketing.state.MarketingIntent
import io.mockk.coEvery
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
class MarketingViewModelTest {

    private val getMarketingMaterialsUseCase: GetMarketingMaterialsUseCase = mockk()
    private val getMarketingMaterialDetailUseCase: GetMarketingMaterialDetailUseCase = mockk()
    private lateinit var viewModel: MarketingViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        
        // Mock init call
        coEvery { getMarketingMaterialsUseCase(any()) } returns flowOf(ResultState.Loading)
        
        viewModel = MarketingViewModel(getMarketingMaterialsUseCase, getMarketingMaterialDetailUseCase)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load materials successful, state should have grouped data`() = runTest {
        val response = mapOf("Brosur" to listOf(mockk<MarketingMaterialResponse>()))
        coEvery { getMarketingMaterialsUseCase(any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(MarketingIntent.LoadMarketingMaterials())

        assertEquals(response, viewModel.state.value.materials)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when load detail successful, state should have selected material`() = runTest {
        val material = mockk<MarketingMaterialResponse>()
        coEvery { getMarketingMaterialDetailUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(material)
        )

        viewModel.processIntent(MarketingIntent.LoadMarketingMaterialDetail(1))

        assertEquals(material, viewModel.state.value.selectedDetail)
        assertEquals(false, viewModel.state.value.isLoading)
    }
}
