package com.nusatim.partner.features.finance.ui.withdrawals

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.finance.databinding.FragmentWithdrawalsBinding
import com.nusatim.partner.features.finance.ui.withdrawals.state.WithdrawalsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.*

@AndroidEntryPoint
class WithdrawalsFragment : BaseFragment<FragmentWithdrawalsBinding>() {

    private val viewModel: WithdrawalsViewModel by viewModels()
    private val adapter by lazy {
        WithdrawalsAdapter { withdrawal ->
            findNavController().navigate("partner://withdrawals/detail?id=${withdrawal.id}".toUri())
        }
    }

    override fun onInitViews() {

        binding.rvWithdrawals.layoutManager = LinearLayoutManager(requireContext())
        binding.rvWithdrawals.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(WithdrawalsIntent.LoadWithdrawals())
            viewModel.processIntent(WithdrawalsIntent.LoadBalance)
        }

        binding.fabRequest.setOnClickListener {
            findNavController().navigate("partner://withdrawals/add".toUri())
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    binding.swipeRefresh.isRefreshing = state.isLoading
                    adapter.submitList(state.withdrawalsResponse?.data)
                    
                    val isEmpty = state.withdrawalsResponse?.data.isNullOrEmpty() && !state.isLoading
                    binding.layoutEmpty.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                    binding.rvWithdrawals.visibility = if (isEmpty) android.view.View.GONE else android.view.View.VISIBLE

                    state.balance?.let { balance ->
                        binding.tvAvailableBalance.text = formatRupiah(balance.availableBalance)
                        binding.tvMinWithdrawal.text = getString(com.nusatim.partner.features.finance.R.string.withdrawal_label_min, formatRupiah(balance.minimumWithdrawal))
                        
                        // FAB Visibility logic
                        binding.fabRequest.visibility = if (balance.availableBalance >= balance.minimumWithdrawal) android.view.View.VISIBLE else android.view.View.GONE
                    }
                }
            }
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }
}
