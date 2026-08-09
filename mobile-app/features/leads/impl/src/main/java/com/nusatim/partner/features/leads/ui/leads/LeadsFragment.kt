package com.nusatim.partner.features.leads.ui.leads

import androidx.appcompat.widget.SearchView
import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentLeadsBinding
import com.nusatim.partner.features.leads.ui.shared.SalesViewModel
import com.nusatim.partner.features.leads.ui.leads.state.LeadsEffect
import com.nusatim.partner.features.leads.ui.leads.state.LeadsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class LeadsFragment : BaseFragment<FragmentLeadsBinding>() {

    private val viewModel: LeadsViewModel by viewModels()
    private val salesViewModel: SalesViewModel by viewModels({ requireParentFragment() })
    private val adapter by lazy {
        LeadsAdapter { lead ->
            findNavController().navigate("partner://leads/detail?id=${lead.id}".toUri())
        }
    }

    override fun onInitViews() {
        binding.rvLeads.layoutManager = LinearLayoutManager(requireContext())
        binding.rvLeads.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(LeadsIntent.LoadLeads())
        }

        binding.fabAdd.setOnClickListener {
            findNavController().navigate("partner://leads/add".toUri())
        }

        binding.btnAddEmpty.setOnClickListener {
            findNavController().navigate("partner://leads/add".toUri())
        }

        // Toolbar setup removed, handled by parent
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    salesViewModel.searchQuery.collect { query ->
                        viewModel.processIntent(LeadsIntent.SearchLeads(query))
                    }
                }
                launch {
                    viewModel.state.collect { state ->
                        showLoading(state.isLoading)
                        binding.swipeRefresh.isRefreshing = false
                        adapter.submitList(state.leadsResponse?.data)
                        
                        val isEmpty = state.leadsResponse?.data.isNullOrEmpty() && !state.isLoading
                        binding.layoutEmpty.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                        binding.rvLeads.visibility = if (isEmpty) android.view.View.GONE else android.view.View.VISIBLE
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is LeadsEffect.ShowError -> {
                                // Show error toast or snackbar
                            }
                            else -> {}
                        }
                    }
                }
            }
        }
    }
}
