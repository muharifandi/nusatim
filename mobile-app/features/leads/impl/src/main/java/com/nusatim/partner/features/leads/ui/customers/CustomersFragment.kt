package com.nusatim.partner.features.leads.ui.customers

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentCustomersBinding
import com.nusatim.partner.features.leads.ui.customers.state.CustomersEffect
import com.nusatim.partner.features.leads.ui.customers.state.CustomersIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class CustomersFragment : BaseFragment<FragmentCustomersBinding>() {

    private val viewModel: CustomersViewModel by viewModels()
    private val adapter by lazy {
        CustomersAdapter { customer ->
            findNavController().navigate("partner://customers/detail?id=${customer.id}".toUri())
        }
    }

    override fun onInitViews() {
        binding.rvCustomers.layoutManager = LinearLayoutManager(requireContext())
        binding.rvCustomers.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(CustomersIntent.LoadCustomers())
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        binding.swipeRefresh.isRefreshing = state.isLoading
                        adapter.submitList(state.customersResponse?.data)
                        
                        val isEmpty = state.customersResponse?.data.isNullOrEmpty() && !state.isLoading
                        binding.layoutEmpty.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                        binding.rvCustomers.visibility = if (isEmpty) android.view.View.GONE else android.view.View.VISIBLE
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is CustomersEffect.ShowError -> {
                                // Show error
                            }
                        }
                    }
                }
            }
        }
    }
}
