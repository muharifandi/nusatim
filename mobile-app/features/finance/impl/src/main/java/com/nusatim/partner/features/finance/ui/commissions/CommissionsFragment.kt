package com.nusatim.partner.features.finance.ui.commissions

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.finance.databinding.FragmentCommissionsBinding
import com.nusatim.partner.features.finance.ui.commissions.state.CommissionsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class CommissionsFragment : BaseFragment<FragmentCommissionsBinding>() {

    private val viewModel: CommissionsViewModel by viewModels()
    private val adapter by lazy {
        CommissionsAdapter { commission ->
            findNavController().navigate("partner://commissions/detail?id=${commission.id}".toUri())
        }
    }

    override fun onInitViews() {
        binding.rvCommissions.layoutManager = LinearLayoutManager(requireContext())
        binding.rvCommissions.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(CommissionsIntent.LoadCommissions(status = getSelectedStatus()))
        }

        setupFilters()
    }

    private fun setupFilters() {
        binding.cgStatusFilters.setOnCheckedStateChangeListener { group, checkedIds ->
            val status = getSelectedStatus()
            viewModel.processIntent(CommissionsIntent.LoadCommissions(status = status))
        }
    }

    private fun getSelectedStatus(): String? {
        return when (binding.cgStatusFilters.checkedChipId) {
            com.nusatim.partner.features.finance.R.id.chip_pending -> "pending"
            com.nusatim.partner.features.finance.R.id.chip_approved -> "approved"
            com.nusatim.partner.features.finance.R.id.chip_paid -> "paid"
            com.nusatim.partner.features.finance.R.id.chip_rejected -> "rejected"
            else -> null
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    binding.swipeRefresh.isRefreshing = state.isLoading
                    adapter.submitList(state.commissionsResponse?.data)
                    
                    val isEmpty = state.commissionsResponse?.data.isNullOrEmpty() && !state.isLoading
                    binding.layoutEmpty.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                    binding.rvCommissions.visibility = if (isEmpty) android.view.View.GONE else android.view.View.VISIBLE
                }
            }
        }
    }
}
